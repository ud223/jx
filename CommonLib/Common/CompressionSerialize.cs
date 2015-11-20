using System;
using System.Data;
using System.Security;
using System.Security.AccessControl;
using System.Runtime.Serialization.Formatters;
using System.Runtime.Serialization.Formatters.Soap;
using System.Runtime.Serialization.Formatters.Binary;
using System.Xml.Serialization;
using System.IO;
using System.Runtime.InteropServices;
using System.Security.Cryptography;

namespace CommonLib.Common
{
    public class CompressionSerialize : BaseClass
    {
        /// <summary>
        /// DataSet序列化并压缩
        /// </summary>
        /// <param name="ds"></param>
        /// <returns></returns>
        public static byte[] CompressDataSet(DataSet ds)
        {
            byte[] compressedBuf;


            #region serialize
            RawSerializer rs = new RawSerializer();
            byte[] buf = rs.Serialize(ds);
            #endregion

            System.IO.MemoryStream ms = new System.IO.MemoryStream();
            System.IO.Compression.GZipStream gs = new System.IO.Compression.GZipStream(ms, System.IO.Compression.CompressionMode.Compress, true);

            gs.Write(buf, 0, buf.Length);
            gs.Close();

            compressedBuf = ms.ToArray();

            return compressedBuf;

        }

        /// <summary>
        /// 对象序列化并压缩
        /// </summary>
        /// <param name="ds"></param>
        /// <returns></returns>
        public static byte[] Compress(object obj, Type type)
        {
            byte[] compressedBuf;
            CompressionSerialize compressionSerialize = new CompressionSerialize();
            
            #region serialize
            byte[] buf = compressionSerialize.Serialize(obj, type);
            #endregion

            System.IO.MemoryStream ms = new System.IO.MemoryStream();
            System.IO.Compression.GZipStream gs = new System.IO.Compression.GZipStream(ms, System.IO.Compression.CompressionMode.Compress, true);

            gs.Write(buf, 0, buf.Length);
            gs.Close();

            compressedBuf = ms.ToArray();

            return compressedBuf;

        }

        /// <summary>
        /// DataSet解压缩并反序列化
        /// </summary>
        /// <param name="buffer"></param>
        /// <returns></returns>
        public static DataSet DecompressDataSet(byte[] buffer)
        {
            System.IO.MemoryStream ms3 = new System.IO.MemoryStream();
            System.IO.MemoryStream ms2 = new System.IO.MemoryStream(buffer);
            System.IO.Compression.GZipStream gs = new System.IO.Compression.GZipStream(ms2, System.IO.Compression.CompressionMode.Decompress);

            byte[] writeData = new byte[4096];

            while (true)
            {
                int size = gs.Read(writeData, 0, writeData.Length);
                if (size > 0)
                {
                    ms3.Write(writeData, 0, size);
                }
                else
                {
                    break;
                }
            }

            gs.Close();
            ms3.Flush();
            byte[] DecompressBuf = ms3.ToArray();

            #region deserialize
            RawDeserializer rd = new RawDeserializer();
            return rd.Deserialize(DecompressBuf);

            #endregion
        }

        /// <summary>
        /// 对象解压缩并反序列化
        /// </summary>
        /// <param name="buffer"></param>
        /// <returns></returns>
        public static object Decompress(byte[] buffer, Type type)
        {
            System.IO.MemoryStream ms3 = new System.IO.MemoryStream();
            System.IO.MemoryStream ms2 = new System.IO.MemoryStream(buffer);
            System.IO.Compression.GZipStream gs = new System.IO.Compression.GZipStream(ms2, System.IO.Compression.CompressionMode.Decompress);

            byte[] writeData = new byte[4096];

            while (true)
            {
                int size = gs.Read(writeData, 0, writeData.Length);
                if (size > 0)
                {
                    ms3.Write(writeData, 0, size);
                }
                else
                {
                    break;
                }
            }

            gs.Close();
            ms3.Flush();
            byte[] DecompressBuf = ms3.ToArray();

            #region deserialize
            CompressionSerialize compressionSerialize = new CompressionSerialize();
            return compressionSerialize.Deserialize(DecompressBuf);

            #endregion
        }

        public byte[] Serialize(object obj, Type type)
        {
            XmlSerializer xmlSerializer = new XmlSerializer(type);
            System.IO.MemoryStream memStream = new System.IO.MemoryStream();
            byte[] b = null;

            try
            {
                xmlSerializer.Serialize(memStream, obj);//内存流二级制序列化
                memStream.Position = 0;
                b = memStream.ToArray();
            }
            catch (Exception ex)
            {
                this.Message = ex.Message;
                this.Result = false;
            }
            finally
            {
                memStream.Close();
                memStream.Dispose();
            }

            return b;
        }

        public object Deserialize(byte[] buf)
        {
            if (buf == null || buf.Length == 0) 
                return null;
            
            System.IO.MemoryStream memStream = new System.IO.MemoryStream();
            object newobj = null;

            try
            {
                memStream.Write(buf, 0, buf.Length);
                memStream.Position = 0;
                System.Runtime.Serialization.Formatters.Binary.BinaryFormatter deserializer =
                new System.Runtime.Serialization.Formatters.Binary.BinaryFormatter();
                newobj = deserializer.Deserialize(memStream);
            }
            catch (Exception ex)
            {
                this.Message = ex.Message;
                this.Result = false;
            }
            finally
            {
                memStream.Close();
            }
                
            return newobj;
        }
        /// <summary>
        /// 获取转换为md5字符
        /// </summary>
        /// <param name="str"></param>
        /// <returns></returns>
        public string GetMD5String(string str)
        {
            MD5 md5 = new MD5CryptoServiceProvider();

            byte[] result = md5.ComputeHash(System.Text.Encoding.Default.GetBytes(str));

            return System.Text.Encoding.Default.GetString(result);
        }
    }
}
